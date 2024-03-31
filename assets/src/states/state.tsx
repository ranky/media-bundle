import { atom, PrimitiveAtom } from 'jotai';
import { Media } from '@rankyMedia/types/Media';
import { LayoutFormViewType } from '@rankyMedia/types/PageLayout';

const dbClickState = atom<boolean>(false);
const currentMediaState = atom<Media | null>(null) as PrimitiveAtom<Media | null>;
const loadingState = atom<boolean>(false);
const pageLayoutState = atom<LayoutFormViewType>('GRID'); // LayoutFormView.GRID
const selectedMediaState = atom<string[]>([]);

export {
  currentMediaState, loadingState, pageLayoutState, selectedMediaState, dbClickState,
};
