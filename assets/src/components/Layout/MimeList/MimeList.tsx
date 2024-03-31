import React from 'react';
import Application from '@rankyMedia/components/Layout/MimeList/Application';
import Text from '@rankyMedia/components/Layout/MimeList/Text';
import Audio from '@rankyMedia/components/Layout/MimeList/Audio';
import Image from '@rankyMedia/components/Layout/MimeList/Image';
import Video from '@rankyMedia/components/Layout/MimeList/Video';
import { MediaItem } from '@rankyMedia/types/Media';

const mimes = {
  application: Application,
  audio: Audio,
  image: Image,
  video: Video,
  text: Text,
};

const MimeList: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  const templateSubType = `${media.file.mimeType}_${media.file.mimeSubType}`;
  const templateType = media.file.mimeType;
  const MimeComponent: React.FC<MediaItem> = mimes[templateSubType] || mimes[templateType] || mimes.application;

  return <MimeComponent media={media} />;
};

export default MimeList;
