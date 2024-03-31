import { Filters } from '@rankyMedia/types/Filters';
import { fetcher } from '@rankyMedia/helpers/fetch';
import useMediaRepository from '@rankyMedia/api/hook/useMediaRepository';
import { useQuery } from 'react-query';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';

const useFilters = () => {
  const mediaRepository = useMediaRepository();
  const { data, error, isLoading } = useQuery<Filters, ApiProblemError>(
    'filters',
    () => fetcher(mediaRepository.getRoutes().filters),
  );

  return {
    data,
    error,
    isLoading,
  };
};

export default useFilters;
