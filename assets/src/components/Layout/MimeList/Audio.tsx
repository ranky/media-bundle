import React from 'react';
import { MediaItem } from '@rankyMedia/types/Media';
import Asset from '@rankyMedia/components/Common/Asset';

const Audio: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  return (
    <img
      loading="lazy"
      aria-hidden="true"
      src={Asset('images/placeholder/audio.jpg')}
      alt={media.description.alt}
      title={`${media.description.title} (${media.file.mime})`}
    />
  );
};

export default Audio;
