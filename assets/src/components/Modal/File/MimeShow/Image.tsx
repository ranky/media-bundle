import React from 'react';
import { MediaItem } from '@rankyMedia/types/Media';
import useSettings from '@rankyMedia/api/hook/useSettings';
import Asset from '@rankyMedia/components/Common/Asset';

const Image: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  const settings = useSettings();

  if (Object.keys(settings.placeholderImageTypes).includes(media.file.mimeSubType)) {
    return (
      <a target="_blank" href={media.file.url} aria-label={`${media.description.title} (opens in a new window)`} rel="noreferrer">
        <img
          src={Asset(settings.placeholderImageTypes[media.file.mimeSubType])}
          data-mime-type={media.file.mimeType}
          data-mime-sub-type={media.file.mimeSubType}
          alt={media.description.alt}
          title={`${media.description.title} (${media.file.mime})`}
        />
      </a>
    );
  }

  if (!settings.supportedImageTypes.includes(media.file.mimeSubType)) {
    return (
      <a target="_blank" href={media.file.url} aria-label={`${media.description.title} (opens in a new window)`} rel="noreferrer">
        <img
          className="ranky-img-placeholder ranky-img-placeholder--show"
          src={Asset('images/placeholder/image.jpg')}
          data-mime-type={media.file.mimeType}
          data-mime-sub-type={media.file.mimeSubType}
          alt={media.description.alt}
          title={`${media.description.title} (${media.file.mime})`}
        />
      </a>
    );
  }

  return (
    <a target="_blank" href={media.file.url} aria-label={`${media.description.title} (opens in a new window)`} rel="noreferrer">
      <img
        src={media.file.url}
        data-mime-type={media.file.mimeType}
        data-mime-sub-type={media.file.mimeSubType}
        alt={media.description.alt}
        title={`${media.description.title}`}
      />
    </a>
  );
};

export default Image;
