import React from 'react';
import { MediaItem } from '@rankyMedia/types/Media';

const Video: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  return (
    <video controls title={`${media.description.title} (${media.file.mime})`}>
      <source src={media.file.url} type={media.file.mime} />
      <track kind="captions" />
      Sorry, your browser doesn't support embedded videos.
    </video>

  );
};

export default Video;
