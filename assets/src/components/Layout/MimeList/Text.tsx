import React from 'react';
import { MediaItem } from '@rankyMedia/types/Media';
import Asset from '@rankyMedia/components/Common/Asset';

const Text: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  return (
    <img
      loading="lazy"
      aria-hidden="true"
      src={Asset('images/placeholder/text.jpg')}
      alt={media.description.alt}
      title={`${media.description.title} (${media.file.mime})`}
    />
  );
};

export default Text;
