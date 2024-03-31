import { useContext } from 'react';
import MediaContext from '@rankyMedia/context/MediaContext';

const UseSettings = () => {
  return useContext(MediaContext).settings;
};
export default UseSettings;
