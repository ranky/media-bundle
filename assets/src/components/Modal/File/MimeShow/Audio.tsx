import React from 'react';
import { MediaItem } from '@rankyMedia/types/Media';

const Audio: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  return (
    <audio controls src={media.file.url} title={`${media.description.title} (${media.file.mime})`}>
      <track kind="captions" />
      Your browser does not support the <code>audio</code> element.
    </audio>
  );
};

export default Audio;
