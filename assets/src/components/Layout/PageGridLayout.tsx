import React from 'react';
import MimeList from '@rankyMedia/components/Layout/MimeList/MimeList';
import { PageLayoutProps } from '@rankyMedia/types/PageLayout';

const PageGridLayout: React.FC<PageLayoutProps> = ({ pages, openModalFile }): React.ReactElement => {
  return (
    <ul
      className="file-list file-list--mode-grid"
      tabIndex={-1}
      role="group"
    >
      {pages && pages.map((page) => {
        return page.result.map((media) => {
          return (
            <li key={media.id} data-id={media.id}>
              <div
                className={`file-item mime-type-${media.file.mimeType} mime-subtype-${media.file.mimeSubType}`}
                role="button"
                aria-label={media.description.alt}
                tabIndex={0}
                onClick={() => openModalFile(media.id)}
                onKeyDown={() => openModalFile(media.id)}
              >
                <MimeList media={media} />
              </div>
            </li>
          );
        });
      })}
    </ul>
  );
};

export default PageGridLayout;
