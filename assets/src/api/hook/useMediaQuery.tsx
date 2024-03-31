import { fetcherWithQueryString } from '@rankyMedia/helpers/fetch';
import { useInfiniteQuery, useQueryClient } from 'react-query';
import { Media, MediaQueryType, MediaResultType } from '@rankyMedia/types/Media';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';

const useMediaQuery = () => {
  const queryClient = useQueryClient();
  const mediaRepository = useMediaRepository();

  const useList = () => {
    return useInfiniteQuery(
      ['media', 'list'],
      ({ pageParam = 1 }) => fetcherWithQueryString(mediaRepository.getRoutes().all, pageParam || 1),
      {
        getNextPageParam: (lastPage): number => {
          const currentPage = lastPage.pagination.page;
          return currentPage < lastPage.pagination.pages ? currentPage + 1 : null;
        },

      },
    );
  };

  function list() {
    return queryClient.getQueryData<MediaQueryType>(['media', 'list']);
  }

  function size() {
    const data = list();
    return data.pages.map((page) => {
      return page.result.length;
    }).reduce((a, b) => a + b);
  }

  function all() {
    const data = list();
    return data.pages.map((page) => {
      return page.result;
    }).flat();
  }

  function find(id: string) {
    const data = list();
    return data?.pages.reduce<Media>((prev, page) => {
      return page.result.find((media) => media.id === id) || prev;
    }, { } as Media);
  }

  function findIndex(id: string) {
    const data = list();
    let indexMedia = { page: 0, index: 0 };
    data?.pages.forEach((page, index) => {
      const hasIndex = page.result.findIndex((media) => media.id === id);
      if (hasIndex > -1) {
        indexMedia = { page: index, index: hasIndex };
      }
    });

    return indexMedia;
  }

  function add(currentMedia: Media) {
    return queryClient.setQueryData<MediaQueryType>(['media', 'list'], (prevData) => {
      return {
        ...prevData,
        pages: prevData.pages.map((page, index) => {
          if (index === 0) {
            const result = page.result.slice();
            result.unshift(currentMedia);
            return {
              result,
              pagination: page.pagination,
            };
          }

          return page;
        }),
      };
    });
  }

  function update(currentMedia: Media) {
    return queryClient.setQueryData<MediaQueryType>(['media', 'list'], (prevData) => {
      return {
        ...prevData,
        pages: prevData.pages.map((page) => {
          return {
            result: page.result.map((media) => (media.id === currentMedia.id ? currentMedia : media)),
            pagination: page.pagination,
          };
        }),
      };
    });
  }

  function reset(data: MediaResultType) {
    return queryClient.setQueryData<MediaQueryType>(['media', 'list'], {
      pages: [data],
      pageParams: [],
    });
  }

  function remove(currentMedia: Media) {
    return queryClient.setQueryData<MediaQueryType>(['media', 'list'], (prevData) => {
      return {
        ...prevData,
        pages: prevData.pages.map((page) => {
          return {
            result: page.result.filter((media) => media.id !== currentMedia.id),
            pagination: page.pagination,
          };
        }),
      };
    });
  }

  return {
    useList,
    list,
    size,
    add,
    all,
    update,
    find,
    findIndex,
    reset,
    remove,
  };
};

export default useMediaQuery;
