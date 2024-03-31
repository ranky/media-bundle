import React from 'react';
import Application from '@rankyMedia/components/Modal/File/MimeShow/Application';
import Text from '@rankyMedia/components/Modal/File/MimeShow/Text';
import Audio from '@rankyMedia/components/Modal/File/MimeShow/Audio';
// import Image from '@rankyMedia/components/Modal/File/MimeShow/Image';
import Video from '@rankyMedia/components/Modal/File/MimeShow/Video';
import { MediaItem } from '@rankyMedia/types/Media';
import ImageResponsive from '@rankyMedia/components/Modal/File/MimeShow/ImageResponsive';
import ImageSvg from '@rankyMedia/components/Modal/File/MimeShow/ImageSvg';

const mimes = {
  application: Application,
  audio: Audio,
  image: ImageResponsive,
  'image_svg+xml': ImageSvg,
  video: Video,
  text: Text,
};

const MimeShow: React.FC<MediaItem> = ({ media }): React.ReactElement => {
  const templateSubType = `${media.file.mimeType}_${media.file.mimeSubType}`;
  const templateType = media.file.mimeType;
  const MimeComponent: React.FC<MediaItem> = mimes[templateSubType] || mimes[templateType] || mimes.application;

  return <MimeComponent media={media} />;
};

export default MimeShow;
