import { MediaResultType } from '@rankyMedia/types/Media';
import PageSelectableLayout from '@rankyMedia/components/Layout/PageSelectableLayout';
import PageGridLayout from '@rankyMedia/components/Layout/PageGridLayout';
import PageListLayout from '@rankyMedia/components/Layout/PageListLayout';

export const LayoutFormView = {
  SELECTABLE: 'SELECTABLE',
  LIST: 'LIST',
  GRID: 'GRID',
} as const;

export type LayoutFormViewType = keyof typeof LayoutFormView;

export const LayoutType = {
  [LayoutFormView.SELECTABLE]: PageSelectableLayout,
  [LayoutFormView.GRID]: PageGridLayout,
  [LayoutFormView.LIST]: PageListLayout,
};

export type PageLayoutProps = {
  pages: Array<MediaResultType>;
  openModalFile: (id: string) => void; // or sidebar
};
