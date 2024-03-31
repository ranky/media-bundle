import { useContext } from 'react';
import MediaContext from '@rankyMedia/context/MediaContext';

const useMediaRepository = () => {
  return useContext(MediaContext).mediaRepository;
};

export default useMediaRepository;
