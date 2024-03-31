import React, { useEffect, useRef } from 'react';
import '@rankyMedia/styles/media_modal.scss';
import MediaManager from '@rankyMedia/MediaManager';
import appConfig from '@rankyMedia/config';
import { dbClickState, selectedMediaState } from '@rankyMedia/states/state';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';
import { getRootElement } from '@rankyMedia/helpers/react';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';
import { errorAlert } from '@rankyMedia/helpers/swal';
import useSettings from '@rankyMedia/api/hook/useSettings';
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import { SelectedMediaDetailEvent } from '@rankyMedia/types/Events';
import { Media } from '@rankyMedia/types/Media';
import { useAtom } from 'jotai';

type MediaFileManagerModalProps = {
  onCloseModal?: () => void;
  onInsertSelection?: (medias: Media[], targetRef: HTMLElement) => void;
};

const MediaManagerModal = ({ onCloseModal, onInsertSelection }: MediaFileManagerModalProps): React.ReactElement => {
  const [selectedMedia, setSelectedMedia] = useAtom(selectedMediaState);
  const [dbClick, setDbClick] = useAtom(dbClickState);
  const mediaQuery = useMediaQuery();
  const modalRef = useRef(null);
  const settings = useSettings();
  const translator = useTranslator();
  const insertButtonRef = useRef<HTMLButtonElement>(null);
  let messageOnSelection = translator.trans('selection_mode.zero_element');

  if (selectedMedia.length > 1) {
    messageOnSelection = translator.trans('selection_mode.multiple_elements', { length: selectedMedia.length });
  } else if (selectedMedia.length === 1) {
    messageOnSelection = translator.trans('selection_mode.one_element');
  }

  useEffect(() => {
    if (dbClick === true) {
      if (insertButtonRef.current) {
        insertButtonRef.current.click();
      }
      setDbClick(false);
    }
  }, [dbClick, setDbClick]);

  function cleanSelectedMedia() {
    setSelectedMedia([]);
  }

  function onClose() {
    if (typeof onCloseModal === 'function') {
      onCloseModal();
    } else {
      modalRef.current.remove();
    }
    setTimeout(async () => {
      cleanSelectedMedia();
    }, 100);
  }

  function insertSelectedMedia(event: React.MouseEvent<HTMLButtonElement, MouseEvent>) {
    // publish('ranky-media:insert-selected-media', selectedMedia);
    const medias = selectedMedia.map((id: string) => {
      return mediaQuery.find(id);
    });
    let targetRef: HTMLElement;
    if (settings.targetRef) {
      targetRef = settings.targetRef;
    } else {
      const rootElement = getRootElement(event.target);
      if (!rootElement) {
        const apiProblem = new ApiProblemError(
          'Root element not found in the hierarchy component tree',
          404,
        );
        errorAlert(apiProblem);
        return;
      }
      targetRef = rootElement;
    }

    if (typeof onInsertSelection === 'function') {
      onInsertSelection(medias, targetRef);
    }
    const eventOnInsert = new CustomEvent<SelectedMediaDetailEvent>('ranky-media:selected-media', {
      detail: { medias },
    });
    targetRef.dispatchEvent(eventOnInsert);
    cleanSelectedMedia();
    onClose();
  }

  return (
    <div className="ranky-media-modal ranky-media-modal--show" ref={modalRef}>
      <div className="ranky-media-modal__dialog" role="document">
        <div className="ranky-media-modal__dialog__title">
          <h2>{settings.title || 'Media File Manager'}</h2>
        </div>
        <button
          onClick={onClose}
          title={translator.trans('modal_close')}
          type="button"
          className="ranky-media-modal__btn-media-close js-close-modal"
          aria-label={translator.trans('modal_close')}
        >
          <span aria-hidden="true">×</span>
        </button>
        <div className="ranky-media-modal__content">
          <div className={appConfig.root_class.slice(1)}>
            <MediaManager />
          </div>
        </div>
        <div className="ranky-media-modal__dialog__footer">
          <div className="ranky-media-modal__dialog__footer__info">
            <p>
              <b>
                {messageOnSelection}
              </b>
            </p>
            <p>
              <button type="button" onClick={cleanSelectedMedia}>
                {translator.trans('selection_mode.clean')}
              </button>
            </p>
          </div>
          <div className="ranky-media-modal__dialog__footer__actions">
            <p>
              <button
                ref={insertButtonRef}
                className="ranky-media-modal__insert-action"
                type="button"
                onClick={(event) => insertSelectedMedia(event)}
              >
                {translator.trans('selection_mode.insert')}
              </button>
            </p>
          </div>
        </div>
      </div>
      <div className="ranky-media-modal__backdrop" />
    </div>
  );
};

export default MediaManagerModal;
