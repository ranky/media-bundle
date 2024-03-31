import React from 'react';
import Trans from '@rankyMedia/components/Common/Trans';
import { MediaQueryType } from '@rankyMedia/types/Media';
import useMediaQuery from '@rankyMedia/api/hook/useMediaQuery';

type Props = {
  data: MediaQueryType;
  hasNextPage: boolean;
  isFetchingNextPage: boolean;
  loadMore: () => void
};

const Pagination: React.FC<Props> = ({
  data, loadMore, hasNextPage, isFetchingNextPage,
}): React.ReactElement => {
  const mediaQuery = useMediaQuery();
  const size = mediaQuery.size();
  const { total } = data.pages[0].pagination;

  return (
    <div className="pagination-media">
      <p className="pagination-media__info">
        <Trans message="pagination_info" data={{ current: size, total }} />
      </p>
      <button
        type="button"
        disabled={!hasNextPage || isFetchingNextPage}
        className="pagination-media__button"
        onClick={() => loadMore()}
      >
        <Trans message="pagination_button_load" />
      </button>
    </div>
  );
};

export default Pagination;
