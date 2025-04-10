import React, {
  Suspense, useCallback, useLayoutEffect, useRef, useState,
} from 'react';
import Asset from '@rankyMedia/components/Common/Asset';
import '@rankyMedia/styles/predefined_form_type.scss';
import { Media, MediaResultType } from '@rankyMedia/types/Media';
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import useSettings from '@rankyMedia/api/hook/useSettings';
import MimeList from '@rankyMedia/components/Layout/MimeList/MimeList';
import { errorAlert } from '@rankyMedia/helpers/swal';
import Loader from '@rankyMedia/components/Common/Loader/Loader';
import { FetcherProps } from '@rankyMedia/api/repository/BaseRepository';
import FilterOperator from '@rankyMedia/types/FilterOperator';
import useOnInsertSelection from '@rankyMedia/hooks/useOnInsertSelection';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';

type PropsType = {
  predefinedData: null | string | [];
  fieldId: string;
  previewJustification?: string;
  buttonJustification?: string;
};

const MediaManagerModal = React.lazy(() => import('@rankyMedia/MediaManagerModal'));

const MediaFormPreview = ({
  predefinedData = null,
  previewJustification = 'center',
  buttonJustification = 'flex-end',
  fieldId,
}: PropsType): React.ReactElement => {
  const previewRef = useRef<HTMLInputElement>();
  const [showModalMediaFileManager, setShowModalMediaFileManager] = useState<boolean>(false);
  const translator = useTranslator();
  const settings = useSettings();
  const mediaRepository = useMediaRepository();
  const [isLoading, setIsLoading] = useState<boolean>(false);

  const onCloseMediaFileManager = useCallback(() => setShowModalMediaFileManager(false), []);
  const { selectedMedia, setSelectedMedia, onInsertSelection } = useOnInsertSelection(fieldId);

  useLayoutEffect(() => {
    const getMedia = async () => {
      setIsLoading(true);
      let callback: (value: string) => Promise<FetcherProps<Media | MediaResultType>>;
      let queryParams = predefinedData as string;

      if (settings.isMultipleSelection) {
        let parseQueryParams: string[];
        try {
          parseQueryParams = JSON.parse(predefinedData as string);
        } catch (e) {
          parseQueryParams = [];
        }
        if (parseQueryParams.length === 0) {
          setIsLoading(false);
          return null;
        }
        queryParams = `?page[disable]=1&filters[id][${FilterOperator.INCLUDES}]=${parseQueryParams.join(',')}`;
        callback = (value: string) => mediaRepository.filter<MediaResultType>(value);
      } else {
        callback = (value: string) => mediaRepository.get<Media>(value);
      }

      const { data, error } = await callback(queryParams);

      if (error) {
        console.log(error);
        setIsLoading(false);
        return errorAlert(error);
      }
      setSelectedMedia('result' in data ? data.result : [data]);
      setIsLoading(false);
      return null;
    };

    if (predefinedData) {
      getMedia();
    }
  }, [mediaRepository, predefinedData, setIsLoading, setSelectedMedia, settings]);

  if (isLoading) {
    return (<Loader />);
  }

  function openMediaFileManager() {
    setShowModalMediaFileManager(true);
  }

  function cleanSelection() {
    setSelectedMedia([]);
    const inputValue = previewRef.current.parentElement.parentElement.querySelector(`input#${fieldId}`) as HTMLInputElement;
    inputValue.value = '';
  }

  return (
    <>
      <div ref={previewRef} className="ranky-media-form-type__content__preview" style={{ justifyContent: previewJustification }}>
        {selectedMedia.length === 0
          ? (
            <div tabIndex={0} role="button" onClick={openMediaFileManager} onKeyDown={openMediaFileManager}>
              <img src={Asset('images/choose-image.png')} alt="Default image. Select a media file" />
            </div>
          )
          : (selectedMedia.map((media: Media) => {
            return (
              <div
                key={`wrapper-selected-${fieldId}-${media.id}`}
                tabIndex={0}
                role="button"
                onClick={openMediaFileManager}
                onKeyDown={openMediaFileManager}
              >
                <MimeList media={media} />
              </div>
            );
          })
          )}
      </div>
      <div className="ranky-media-form-type__content__wrapper_button" style={{ justifyContent: buttonJustification }}>
        <button
          className="clean-selected-media-files"
          type="button"
          title={translator.trans('form_type.clean_selection_button')}
          onClick={cleanSelection}
        >
          {translator.trans('form_type.clean_selection_button')}
        </button>
        <button
          type="button"
          title={translator.trans('form_type.open_selection_button')}
          onClick={openMediaFileManager}
        >
          {translator.trans('form_type.open_selection_button')}
        </button>
      </div>
      <Suspense fallback="">
        {showModalMediaFileManager
          ? (
            <MediaManagerModal onInsertSelection={onInsertSelection} onCloseModal={onCloseMediaFileManager} />
          ) : null}
      </Suspense>
    </>
  );
};

export default MediaFormPreview;
