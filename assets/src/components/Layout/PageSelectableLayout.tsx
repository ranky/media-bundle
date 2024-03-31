import React from 'react';
import MimeList from '@rankyMedia/components/Layout/MimeList/MimeList';
import { PageLayoutProps } from '@rankyMedia/types/PageLayout';
import { dbClickState, selectedMediaState } from '@rankyMedia/states/state';
import useSettings from '@rankyMedia/api/hook/useSettings';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';
import { Media } from '@rankyMedia/types/Media';
import { useAtom } from 'jotai';

const PageSelectableLayout: React.FC<PageLayoutProps> = ({ pages, openModalFile }): React.ReactElement => {
  const [selectedMedia, setSelectedMedia] = useAtom(selectedMediaState);
  const [, setDbClick] = useAtom(dbClickState);
  const [lastSelected, setLastSelected] = React.useState<string | null>(null);
  const settings = useSettings();
  const mediaQuery = useMediaQuery();

  function onDoubleClick(event: React.MouseEvent<HTMLLIElement>, mediaId: string) {
    if (!selectedMedia.includes(mediaId)) {
      setSelectedMedia((currentList) => [...currentList, mediaId]);
    }
    setLastSelected(mediaId);
    setDbClick(true);
  }

  function onSelectMedia(event: React.MouseEvent<HTMLInputElement> | React.ChangeEvent<HTMLInputElement>, id: string) {
    if (settings.isMultipleSelection === false) {
      setSelectedMedia([id]);
      return;
    }
    if ('shiftKey' in event.nativeEvent && event.nativeEvent.shiftKey === true) {
      if (lastSelected === null) {
        setLastSelected(id);
        return;
      }
      const allMedia = mediaQuery.all().map((media: Media) => media.id);
      const start = allMedia.indexOf(id);
      const end = allMedia.indexOf(lastSelected);
      const newSelectedMedia = allMedia.slice(Math.min(start, end), Math.max(start, end) + 1);
      setSelectedMedia(newSelectedMedia);
      setLastSelected(id);
      return;
    }

    if (selectedMedia.includes(id)) {
      setSelectedMedia((currentList) => currentList.filter((mediaId) => mediaId !== id));
    } else {
      setSelectedMedia((currentList) => [...currentList, id]);
    }
    setLastSelected(id);
  }

  return (
    <ul tabIndex={-1} className="file-list file-list--mode-grid file-list--mode-selectable">
      {pages && pages.map((page) => {
        return page.result.map((media) => {
          return (
            <li key={`li-${media.id}`} data-id={media.id} onDoubleClick={(event) => onDoubleClick(event, media.id)}>
              <div
                className={`file-item mime-type-${media.file.mimeType} mime-subtype-${media.file.mimeSubType}`}
                aria-label={media.description.alt}
                role="checkbox"
                aria-checked={selectedMedia.includes(media.id)}
                tabIndex={0}
              >
                <input
                  tabIndex={-1}
                  type="checkbox"
                  name="media[]"
                  id={`ck-media-${media.id}`}
                  checked={selectedMedia.includes(media.id)}
                  onChange={(event) => onSelectMedia(event, media.id)}
                />
                {/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
                <label htmlFor={`ck-media-${media.id}`}>
                  <MimeList media={media} />
                </label>
                <button onClick={() => openModalFile(media.id)} type="button">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    className="bi bi-pencil"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"
                    />
                  </svg>
                </button>
              </div>
            </li>
          );
        });
      })}
    </ul>
  );
};

export default PageSelectableLayout;
