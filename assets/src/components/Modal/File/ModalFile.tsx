import React from 'react';
import './modal_file.scss';
import ModalFileContent from '@rankyMedia/components/Modal/File/ModalFileContent';
import Trans from '@rankyMedia/components/Common/Trans';
import { confirmDeleteAlert, errorAlert, successAlert } from '@rankyMedia/helpers/swal';
import { currentMediaState } from '@rankyMedia/states/state';
import { useQueryClient } from 'react-query';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';
import { useAtom } from 'jotai';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';

type ModalFileType = {
  onClose: () => void;
};

const ModalFile = ({ onClose }: ModalFileType): React.ReactElement => {
  const [currentMedia, setCurrentMedia] = useAtom(currentMediaState);
  const queryClient = useQueryClient();
  const mediaQuery = useMediaQuery();
  const mediaRepository = useMediaRepository();

  function onNext({ currentTarget }) {
    const data = mediaQuery.list();
    const indexCurrentMedia = mediaQuery.findIndex(currentMedia.id);
    const pages = data.pages.length;
    const hasPages = pages > indexCurrentMedia.page;
    const nextMedia = data.pages[indexCurrentMedia.page]?.result[indexCurrentMedia.index + 1] || null;

    if (nextMedia) {
      setCurrentMedia(nextMedia);
      return currentTarget.removeAttribute('disabled');
    }

    if (hasPages) {
      const nextMediaInNextPage = data.pages[indexCurrentMedia.page + 1]?.result[0] || null;
      if (nextMediaInNextPage) {
        setCurrentMedia(nextMediaInNextPage);
        return currentTarget.removeAttribute('disabled');
      }
    }

    return currentTarget.setAttribute('disabled', '');
  }

  function onPrev({ currentTarget }) {
    const data = mediaQuery.list();
    const indexCurrentMedia = mediaQuery.findIndex(currentMedia.id);
    const prevMedia = data.pages[indexCurrentMedia.page].result[indexCurrentMedia.index - 1];
    const pages = data.pages.length;
    const hasPages = indexCurrentMedia.page > 0 && indexCurrentMedia.page < pages;

    if (prevMedia) {
      setCurrentMedia(prevMedia);
      return currentTarget.removeAttribute('disabled');
    }

    if (hasPages) {
      const prevPageLastIndex = (data.pages[indexCurrentMedia.page - 1].result.length) - 1;
      const prevMediaInPrevPage = data.pages[indexCurrentMedia.page - 1]?.result[prevPageLastIndex] || null;
      if (prevMediaInPrevPage) {
        setCurrentMedia(prevMediaInPrevPage);
        return currentTarget.removeAttribute('disabled');
      }
    }

    return currentTarget.setAttribute('disabled', '');
  }

  const onDelete = async (event: React.MouseEvent<HTMLButtonElement>, setDisableSubmit: (value: boolean) => void) => {
    event.preventDefault();
    const el = event.currentTarget;

    const callbackConfirm = async () => {
      const { id } = el.dataset;
      setDisableSubmit(true);
      const { data, error } = await mediaRepository.delete<{ message: string }>(id);
      if (error) {
        await errorAlert(error);
      } else {
        await queryClient.invalidateQueries('filters');
        await queryClient.invalidateQueries(['media', 'list']);
        await successAlert(data?.message);
        onClose();
      }
      setDisableSubmit(false);
    };

    return confirmDeleteAlert(callbackConfirm);
  };

  return (
    <div className="wrapper-ranky-media-modal-file" key={`ranky-media-modal-file-${currentMedia.id}`}>
      <div
        className="ranky-media-modal-file ranky-media-modal-file--show"
        tabIndex={-1}
        aria-labelledby="ranky-media-modal-file"
        aria-modal="true"
        role="dialog"
      >
        <div className="ranky-media-modal-file__dialog" role="document">
          <div className="ranky-media-modal-file__dialog__header ranky-media-modal-file__dialog--bg-primary-dark">
            <h3 className="ranky-media-modal-file__dialog__header__title">
              <Trans message="modal_title" data={{ file_name: currentMedia.file.name, id: currentMedia.id }} />
            </h3>
            <div className="ranky-media-modal-file__dialog__header__options">
              <ul>
                <li>
                  <button
                    onClick={onPrev}
                    type="button"
                    className="ranky-media-modal-file__btn-media-prev js-prev-modal"
                    aria-label="Prev"
                  >
                    <span aria-hidden="true">←</span>
                  </button>
                </li>
                <li>
                  <button
                    onClick={onNext}
                    type="button"
                    className="ranky-media-modal-file__btn-media-next js-next-modal"
                    aria-label="Next"
                  >
                    <span aria-hidden="true">→</span>
                  </button>
                </li>
                <li>
                  <button
                    onClick={onClose}
                    type="button"
                    className="ranky-media-modal-file__btn-media-close js-close-modal"
                    aria-label="Close"
                  >
                    <span aria-hidden="true">×</span>
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <div className="ranky-media-modal-file__dialog__content">
            <ModalFileContent onDelete={onDelete} />
          </div>
        </div>
      </div>
      <div className="ranky-media-modal-file-backdrop" />
    </div>
  );
};

export default ModalFile;
