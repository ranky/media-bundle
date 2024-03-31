import React from 'react';
import { MediaItem, Thumbnail } from '@rankyMedia/types/Media';
import useSettings from '@rankyMedia/api/hook/useSettings';
import Asset from '@rankyMedia/components/Common/Asset';

const Image: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  const settings = useSettings();

  if (Object.keys(settings.placeholderImageTypes).includes(media.file.mimeSubType)) {
    return (
      <img
        loading="lazy"
        aria-hidden="true"
        src={Asset(settings.placeholderImageTypes[media.file.mimeSubType])}
        data-mime-type={media.file.mimeType}
        data-mime-sub-type={media.file.mimeSubType}
        alt={media.description.alt}
        title={`${media.description.title} (${media.file.mime})`}
      />
    );
  }

  if (!settings.supportedImageTypes.includes(media.file.mimeSubType)) {
    return (
      <img
        loading="lazy"
        aria-hidden="true"
        className="ranky-img-placeholder ranky-img-placeholder--list"
        src={Asset('images/placeholder/image.jpg')}
        data-mime-type={media.file.mimeType}
        data-mime-sub-type={media.file.mimeSubType}
        alt={media.description.alt}
        title={`${media.description.title} (${media.file.mime})`}
      />
    );
  }

  const minWidthThumbnail = media?.thumbnails.reduce((prev: Thumbnail, current: Thumbnail) => {
    if (prev === null) {
      return current;
    }
    return prev.dimension.width < current.dimension.width ? prev : current;
  }, null) ?? media;

  return (
    <img
      loading="lazy"
      aria-hidden="true"
      data-mime-type={media.file.mimeType}
      data-mime-sub-type={media.file.mimeSubType}
      src={'url' in minWidthThumbnail ? minWidthThumbnail.url : media.file.url}
      width={minWidthThumbnail.dimension.width}
      height={minWidthThumbnail.dimension.height}
      alt={media.description.alt}
      title={`${media.description.title} (${media.file.mime})`}
    />
  );
};

export default Image;
