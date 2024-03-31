import React from 'react';
import { MediaItem, Thumbnail } from '@rankyMedia/types/Media';
import { removeLastChar } from '@rankyMedia/helpers/string';
import useSettings from '@rankyMedia/api/hook/useSettings';
import Asset from '@rankyMedia/components/Common/Asset';

const ImageResponsive: React.FC<MediaItem> = ({ media }): React.ReactElement => {
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

  let srcSet = `${media.file.url} ${media.dimension.width}w,`;

  media.thumbnails.forEach((thumbnail: Thumbnail) => {
    srcSet += ` ${thumbnail.url} ${thumbnail.dimension.width}w,`;
  });

  return (
    <a target="_blank" href={media.file.url} aria-label={`${media.description.title} (opens in a new window)`} rel="noreferrer">
      <img
        src={media.file.url}
        data-mime-type={media.file.mimeType}
        data-mime-sub-type={media.file.mimeSubType}
        width={media.dimension.width ?? 'auto'}
        height={media.dimension.height ?? 'auto'}
        srcSet={media.dimension.width ? removeLastChar(srcSet, ',') : ''}
        alt={media.description.alt}
        title={`${media.description.title}`}
      />
    </a>
  );
};

export default ImageResponsive;
