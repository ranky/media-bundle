import React, { useState } from 'react';
import MimeList from '@rankyMedia/components/Layout/MimeList/MimeList';
import { confirmDeleteAlert, errorAlert, successAlert } from '@rankyMedia/helpers/swal';
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import { useQueryClient } from 'react-query';
import { loadingState } from '@rankyMedia/states/state';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';
import { PageLayoutProps } from '@rankyMedia/types/PageLayout';
import { useSetAtom } from 'jotai';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';

const PageListLayout: React.FC<PageLayoutProps> = ({ pages, openModalFile }): React.ReactElement => {
  const [selectAll, setSelectAll] = useState<boolean>(false);
  const [selectedList, setSelectedList] = useState<string[]>([]);
  const setIsGlobalLoading = useSetAtom(loadingState);
  const translator = useTranslator();
  const queryClient = useQueryClient();
  const mediaQuery = useMediaQuery();
  const mediaRepository = useMediaRepository();

  const handleAllCheckbox = () => {
    if (selectedList.length > 0) {
      setSelectAll(false);
      setSelectedList([]);
    } else {
      setSelectAll(true);
      setSelectedList(mediaQuery.all().map((media) => media.id));
    }
  };

  const handleSingleCheckbox = (id: string) => {
    if (selectedList.includes(id)) {
      setSelectedList(selectedList.filter((mediaId) => mediaId !== id));
      setSelectAll(false);
    } else {
      const newSelectList = [...selectedList, id];
      setSelectedList(newSelectList);
      setSelectAll(newSelectList.length === mediaQuery.all().length);
    }
  };

  const handleBatchActions = async ({ target }) => {
    if (target.value === '') {
      return {};
    }
    if (selectedList.length === 0) {
      // eslint-disable-next-line no-param-reassign
      target.selectedIndex = 0;
      return errorAlert(new ApiProblemError(translator.trans('bulk_actions_error_no_select')));
    }

    const deleteBulkCallback = async () => {
      setIsGlobalLoading(true);
      const response = await mediaRepository.bulkDelete(selectedList);
      if (response.error) {
        return errorAlert(response.error);
      }
      await queryClient.invalidateQueries(['media', 'list']);
      await queryClient.invalidateQueries('filters');
      target.selectedIndex = 0;
      setSelectAll(false);
      setSelectedList([]);
      setIsGlobalLoading(false);
      return successAlert();
    };

    switch (target.value) {
      case 'delete':
        return confirmDeleteAlert(
          deleteBulkCallback,
          function resetIndex() { target.selectedIndex = 0; },
          translator.trans('bulk_actions_delete_prompt', { value: selectedList.length }),
        );
      default:
        return errorAlert(
          new ApiProblemError(
            translator.trans('bulk_actions_error_no_action', { value: target?.value || 'undefined' }),
          ),
        );
    }
  };

  return (
    <div className="file-list file-list--mode-list">
      <div className="batch-actions">
        <select name="media-batch-actions" id="media-batch-actions" onChange={handleBatchActions}>
          <option value="">{translator.trans('bulk_actions_title')}</option>
          <option value="delete">{translator.trans('bulk_actions_delete')}</option>
        </select>
      </div>
      <div className="table-responsive">
        <table className="table-view-list">
          <thead>
            <tr>
              <th>
                <input
                  type="checkbox"
                  id="ck-media-all"
                  onChange={handleAllCheckbox}
                  checked={selectAll}
                />
              </th>
              <th>{translator.trans('file')}</th>
              <th>{translator.trans('type')}</th>
              <th>{translator.trans('date')}</th>
              <th>{translator.trans('breakpoints')}</th>
            </tr>
          </thead>
          <tbody>
            {pages && pages.map((page) => {
              return page.result.map((media) => {
                return (
                  <tr key={media.id}>
                    <td>
                      <input
                        type="checkbox"
                        name="media[]"
                        id={`ck-media-${media.id}`}
                        onChange={() => handleSingleCheckbox(media.id)}
                        checked={selectedList.includes(media.id)}
                      />
                    </td>
                    <td>
                      <div
                        tabIndex={0}
                        onClick={() => openModalFile(media.id)}
                        onKeyDown={() => openModalFile(media.id)}
                        role="button"
                      >
                        <span className="file-preview">
                          <MimeList media={media} />
                        </span>
                        <span className="file-name">
                          {media.file.name}
                        </span>
                      </div>
                    </td>
                    <td>{media.file.mime} {media?.dimension?.label ? `(${media.dimension.label})` : null}</td>
                    <td>{media.createdAt} {translator.trans('by')} {media.createdBy}</td>
                    <td>{media.thumbnails.length > 0
                      ? (
                        <ul>
                          {media.thumbnails.map((thumbnail) => {
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
                );
              });
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default PageListLayout;
