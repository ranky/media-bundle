import React from 'react';
import { MediaItem } from '@rankyMedia/types/Media';

const ImageSvg: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  return (
    <a target="_blank" href={media.file.url} aria-label={`${media.description.title} (opens in a new window)`} rel="noreferrer">
      <img
        className="img-svg-preview"
        src={media.file.url}
        data-mime-type={media.file.mimeType}
        data-mime-sub-type={media.file.mimeSubType}
        alt={media.description.alt}
        title={`${media.description.title}`}
      />
    </a>
  );
};

export default ImageSvg;
