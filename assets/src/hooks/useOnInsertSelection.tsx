import { useCallback, useState } from 'react';
import { Media } from '@rankyMedia/types/Media';
import useSettings from '@rankyMedia/api/hook/useSettings';

const useOnInsertSelection = (fieldId: string) => {
  const [selectedMedia, setSelectedMedia] = useState<Media[]>([]);
  const settings = useSettings();
  const onInsertSelection = useCallback((medias: Media[], targetRef: HTMLElement) => {
    if (medias.length <= 0) {
      return;
    }
    const inputValue = targetRef.parentElement.querySelector(`input#${fieldId}`) as HTMLInputElement;
    if (settings.isMultipleSelection === false) {
      inputValue.value = medias[0].id || '';
    } else {
      inputValue.value = JSON.stringify(medias.map((media) => media.id)) || JSON.stringify([]);
    }
    setSelectedMedia(medias);
  }, [fieldId, settings]);

  return {
    onInsertSelection,
    selectedMedia,
    setSelectedMedia,
  };
};

export default useOnInsertSelection;
