import React, { useEffect } from 'react';
import { DashboardModal, useUppy as useUppyReact } from '@uppy/react';
import Uppy from '@uppy/core';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import '@uppy/drop-target/dist/style.css';
import './dropzone.scss';
import appConfig from '@rankyMedia/config';
import XHRUpload from '@uppy/xhr-upload';
import { errorAlert } from '@rankyMedia/helpers/swal';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';
import DropTarget from '@uppy/drop-target';
import addScript from '@rankyMedia/helpers/script';
import useSettings from '@rankyMedia/api/hook/useSettings';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';
import { useQueryClient } from 'react-query';

type PropsType = {
  showUppyModal: boolean;
  setShowUppyModal: (show: boolean) => void;
};

const UppyModalDashboard: React.FC<PropsType> = ({ showUppyModal, setShowUppyModal }): React.ReactElement => {
  const settings = useSettings();
  const mediaRepository = useMediaRepository();
  const queryClient = useQueryClient();

  const uppy = useUppyReact(() => {
    const uppyInstance = new Uppy({
      autoProceed: false,
      allowMultipleUploadBatches: true,
      debug: appConfig.env === 'development',
      restrictions: {
        maxFileSize: settings.maxFileSize || 7340032, // 7 MB
        allowedFileTypes: settings.mimeTypes.length ? settings.mimeTypes : null,
      },
      onBeforeFileAdded: () => {
        if (!showUppyModal) {
          setShowUppyModal(true);
        }
        return true;
      },
    });

    uppyInstance.use(XHRUpload, {
      id: 'XHRUpload',
      endpoint: mediaRepository.getRoutes().upload,
      // metaFields: null, // null is all metadata fields,
      timeout: 90 * 1000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    // TODO: add tus upload, caption, title metadata, image editor, unsplash, audio, webcam, url, Golden Retriever
    uppyInstance.on('file-added', (file) => {
      uppyInstance.setFileMeta(file.id, {
        size: file.size,
      });
    });
    uppyInstance.on('restriction-failed', (file, error) => {
      return errorAlert(new ApiProblemError(`<b>${file.name}</b>: ${error}`));
    });
    uppyInstance.on('upload-error', (_file, error, response) => {
      const apiProblemError = (response.body as ApiProblem).title !== undefined
        ? ApiProblemError.fromApiProblem(response.body as ApiProblem)
        : new ApiProblemError(error as unknown as string, response.status);

      return errorAlert(apiProblemError);
    });
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    uppyInstance.on('upload-success', async (_file, _response) => {
      // const media = response.body;
      await queryClient.invalidateQueries('filters');
      await queryClient.invalidateQueries(['media', 'list']);
    });

    return uppyInstance;
  });

  useEffect(() => {
    if (uppy.getPlugin('DropTarget')) {
      return;
    }
    uppy.use(DropTarget, {
      id: 'DropTarget',
      target: appConfig.root_class,
      onDragOver: () => {
        document
          .querySelector('.dropzone-drag-zone')
          ?.setAttribute('style', 'visibility:visible;opacity:1');
      },
      onDrop: () => {
        document
          .querySelector('.dropzone-drag-zone')
          ?.removeAttribute('style');
      },
      onDragLeave: (event) => {
        const target = event.target as HTMLDivElement;
        if (target.classList.contains('dropzone-drag-zone')) {
          document
            .querySelector('.dropzone-drag-zone')
            ?.removeAttribute('style');
        }
      },
    });

    window.uppy = uppy;
    const { locale } = settings;
    let isoCode = locale;
    if (isoCode.indexOf('_') < 0) {
      const pattern = new RegExp(`${isoCode.toLocaleLowerCase()}_`, 'g');
      isoCode = appConfig.uppy_locales.find((uppyLocale) => uppyLocale.match(pattern));
    }
    if (!isoCode) {
      isoCode = 'en_US';
      console.error(`There are no translations for the locale ${locale}. The en_US default translation pack has been loaded.`);
    }

    if (typeof window.Uppy === 'undefined') {
      window.Uppy = {
        locales: {},
      };
    }

    async function addLocaleScript() {
      const localePath = `${appConfig.assets_url_prefix}uppy/locales/`;
      await addScript(`${localePath}${isoCode}.min.js`, () => {
        window.uppy.setOptions({ locale: window.Uppy.locales[isoCode] });
      });
    }

    if (typeof window.Uppy.locales[isoCode] === 'undefined') {
      addLocaleScript();
    } else {
      window.uppy.setOptions({ locale: window.Uppy.locales[isoCode] });
    }
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    // return () => uppy.close({ reason: 'unmount' });
  }, [uppy, settings]);
  return (
    <DashboardModal
      uppy={uppy}
      closeModalOnClickOutside
      disableInformer={false}
      proudlyDisplayPoweredByUppy={false}
      open={showUppyModal}
      onRequestClose={() => setShowUppyModal(false)}
    />
  );
};

export default UppyModalDashboard;
