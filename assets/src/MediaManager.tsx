import React, { Suspense, useState } from 'react';
import '@rankyMedia/styles/index.scss';
import { MediaQueryType } from '@rankyMedia/types/Media';
import Loader from '@rankyMedia/components/Common/Loader/Loader';
import Pagination from '@rankyMedia/components/Common/Pagination';
import FormFilters from '@rankyMedia/components/Form/Filters/FormFilters';
import { currentMediaState, pageLayoutState, loadingState } from '@rankyMedia/states/state';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';
import { useIsMutating } from 'react-query';
import { LayoutType, PageLayoutProps } from '@rankyMedia/types/PageLayout';
import Dropzone from '@rankyMedia/components/Dropzone/Dropzone';
import { useAtomValue, useSetAtom } from 'jotai';

const ModalFile = React.lazy(() => import(
  /* webpackChunkName: "rank_media.modal_file" */
  '@rankyMedia/components/Modal/File/ModalFile'
));

const MediaManager = (): React.ReactElement => {
  const [showModalFile, setShowModalFile] = useState<boolean>(false);
  const { useList } = useMediaQuery();
  const {
    data, fetchNextPage, hasNextPage, isFetchingNextPage, isLoading,
  } = useList();
  const isMutatingMedias = useIsMutating();
  const mediaCrud = useMediaQuery();
  const setCurrentMedia = useSetAtom(currentMediaState);
  const isGlobalLoading = useAtomValue(loadingState);
  const layoutType = useAtomValue(pageLayoutState);
  const LayoutComponent: React.FC<PageLayoutProps> = LayoutType[layoutType];

  if (isLoading) {
    return (<Loader />);
  }

  function openModalFile(id: string) {
    const currentMedia = mediaCrud.find(id);
    setCurrentMedia(currentMedia);
    setShowModalFile(true);
  }

  return (
    <>
      <Dropzone />
      <FormFilters />
      {isMutatingMedias || isGlobalLoading ? <Loader style={{ opacity: 0.9, top: '78px' }} /> : null}
      <div className="ranky-media__items">
        <LayoutComponent
          pages={data?.pages}
          openModalFile={(id) => openModalFile(id)}
        />
        <Pagination
          data={data as MediaQueryType}
          loadMore={fetchNextPage}
          hasNextPage={hasNextPage}
          isFetchingNextPage={isFetchingNextPage}
        />
      </div>
      <Suspense fallback="">
        {showModalFile
          ? (<ModalFile onClose={() => setShowModalFile(false)} />)
          : null}
      </Suspense>
    </>
  );
};

export default MediaManager;
