import { useContext } from 'react';
import MediaContext from '@rankyMedia/context/MediaContext';
import Translator from '../model/Translator';

const UseTranslator = () => {
  return useContext(MediaContext).translator as Translator;
};
export default UseTranslator;
