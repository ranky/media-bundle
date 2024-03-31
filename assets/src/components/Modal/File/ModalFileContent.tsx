import React, { useState } from 'react';
import { Media } from '@rankyMedia/types/Media';
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import Trans from '@rankyMedia/components/Common/Trans';
import MimeShow from '@rankyMedia/components/Modal/File/MimeShow/MimeShow';
import { errorAlert, successAlert } from '@rankyMedia/helpers/swal';
import { currentMediaState } from '@rankyMedia/states/state';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';
import { useAtom } from 'jotai';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';

type ModalFileContentType = {
  onDelete: (event: React.MouseEvent<HTMLButtonElement>, setDisableSubmit: (value: boolean) => void) => void;
};

type ModalFileForm = {
  id: string;
  name: string;
  alt: string;
  title: string;
};

const ModalFileContent = ({ onDelete }: ModalFileContentType): React.ReactElement => {
  const translator = useTranslator();
  const [currentMedia, setCurrentMedia] = useAtom(currentMediaState);
  const mediaRepository = useMediaRepository();
  const mediaQuery = useMediaQuery();
  const [disableSubmit, setDisableSubmit] = useState<boolean>(false);
  const [form, setForm] = useState<ModalFileForm>({
    id: currentMedia.id,
    name: currentMedia.file.basename,
    alt: currentMedia.description.alt,
    title: currentMedia.description.title,
  });

  const onSubmit = async (event: { preventDefault: () => void; }) => {
    event.preventDefault();
    setDisableSubmit(true);
    const { id } = currentMedia;
    const { data, error } = await mediaRepository.put<Media>(id, form);
    if (error) {
      console.log(error);
      setDisableSubmit(false);
      return errorAlert(error);
    }
    setCurrentMedia(data);
    setForm({
      ...form,
      ...{
        name: data.file.basename,
        alt: data.description.alt,
        title: data.description.title,
      },
    });
    mediaQuery.update(data);
    setDisableSubmit(false);
    return successAlert();
  };

  const onChangeHandler = (event) => {
    const element = event.target;
    const userInput = { [element.name]: element.value };
    setForm({ ...form, ...userInput });
  };

  return (
    <div className="ranky-media-modal-file__dialog__content__show">
      <div className="ranky-media-modal-file__dialog__content__show__preview">
        <MimeShow media={currentMedia} />
        <button
          type="button"
          className="ranky-media-modal-file__btn-media-danger js-delete-media"
          data-id={currentMedia.id}
          disabled={disableSubmit}
          onClick={(event) => onDelete(event, setDisableSubmit)}
        >
          🗑️ <Trans message="delete" />
        </button>
      </div>
      <div className="ranky-media-modal-file__dialog__content__show__info">
        <table>
          <tbody>
            <tr>
              <th scope="row" aria-label="form_name"><Trans message="form_name" /></th>
              <td>{currentMedia.file.name}</td>
            </tr>
            <tr>
              <th scope="row">URL</th>
              <td>
                <a target="_blank" href={currentMedia.file.url} rel="noreferrer">
                  {currentMedia.file.url}
                </a>
              </td>
            </tr>
            <tr>
              <th scope="row" aria-label="file_type"><Trans message="file_type" /></th>
              <td>{currentMedia.file.mime}</td>
            </tr>
            {currentMedia?.dimension?.label ? (
              <tr>
                <th scope="row" aria-label="dimensions"><Trans message="dimensions" /></th>
                <td>{currentMedia.dimension.label}</td>
              </tr>
            ) : null}
            <tr>
              <th scope="row" aria-label="size"><Trans message="size" /></th>
              <td>{currentMedia.file.humanSize}</td>
            </tr>
            <tr>
              <th scope="row" aria-label="created_at"><Trans message="created_at" /></th>
              <td>
                {currentMedia.createdAt} <Trans message="by" /> {currentMedia.createdBy}
              </td>
            </tr>
            <tr>
              <th scope="row" aria-label="updated_at"><Trans message="updated_at" /></th>
              <td>
                {currentMedia.updatedAt} <Trans message="by" /> {currentMedia.updatedBy}
              </td>
            </tr>
            <tr>
              <th scope="row">
                <Trans message="breakpoints" />
              </th>
              <td>{currentMedia.thumbnails.length > 0
                ? (
                  <ul>
                    {currentMedia.thumbnails.map((thumbnail) => {
                      return (
                        <li key={thumbnail.name + thumbnail.breakpoint}>
                          <a target="_blank" rel="noopener noreferrer" href={thumbnail.url}>
                            <b>{thumbnail.breakpoint}:</b> {thumbnail.dimension.label}&nbsp;
                            <b>size:</b> {thumbnail.humanSize}
                          </a>
                        </li>
                      );
                    })}
                  </ul>
                ) : '-'}
              </td>
            </tr>
          </tbody>
        </table>
        <form
          name="media"
          id="ranky-media-file__form"
          method="POST"
          autoComplete="off"
          encType="multipart/form-data"
          onSubmit={onSubmit}
        >
          <div className="input-group">
            <label htmlFor="name"><Trans message="form_name" /></label>
            <input type="text" name="name" id="ranky-media-file__form__name" value={form.name} onChange={onChangeHandler} required autoComplete="off" />
            <span className="input-group-text">.{currentMedia.file.extension}</span>
          </div>
          <label htmlFor="alt"><Trans message="form_alt" /></label>
          <input type="text" name="alt" id="ranky-media-file__form__alt" value={form.alt} onChange={onChangeHandler} required />
          <label htmlFor="title"><Trans message="form_title" /></label>
          <input type="text" name="title" id="ranky-media-file__form__title" value={form.title} onChange={onChangeHandler} required />
          <input type="hidden" name="id" id="ranky-media-file__form__id" defaultValue={form.id} />
          <input type="submit" disabled={disableSubmit} value={translator.trans('form_save')} />
        </form>
      </div>
    </div>
  );
};

export default ModalFileContent;
