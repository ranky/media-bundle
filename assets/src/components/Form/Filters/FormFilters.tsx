import React, { useEffect, useRef, useState } from 'react';
import SelectFilter from '@rankyMedia/components/Form/Filters/SelectFilter';
import FilterOperator from '@rankyMedia/types/FilterOperator';
import InputSearchFilter from '@rankyMedia/components/Form/Filters/InputSearchFilter';
import LayoutFilter from '@rankyMedia/components/Form/Filters/LayoutFilter';
import SortFilter from '@rankyMedia/components/Form/Filters/SortFilter';
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import useFilters from '@rankyMedia/api/hook/useFilters';
import Loader from '@rankyMedia/components/Common/Loader/Loader';
import Error from '@rankyMedia/components/Common/Error';
import { loadingState, pageLayoutState } from '@rankyMedia/states/state';
import { LayoutFormView, LayoutFormViewType } from '@rankyMedia/types/PageLayout';
import { useAtom, useSetAtom } from 'jotai';
import { useQueryClient } from 'react-query';

const FormFilters = (): React.ReactElement => {
  const translator = useTranslator();
  const timeoutSearchFilterIdRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const { data: filters, error, isLoading } = useFilters();
  const queryClient = useQueryClient();
  const setIsGlobalLoading = useSetAtom(loadingState);
  const [pageLayout, setPageLayout] = useAtom(pageLayoutState);
  const formWrapperRef = useRef<HTMLDivElement>(null);
  const [refVisible, setRefVisible] = useState(false);

  useEffect(() => {
    if (refVisible) {
      if (formWrapperRef.current && formWrapperRef.current.clientWidth < 990) {
        if (formWrapperRef.current.querySelector<HTMLDivElement>('.view-filter')) {
          formWrapperRef.current.querySelector<HTMLDivElement>('.view-filter').style.margin = 'auto';
        }
        if (formWrapperRef.current.querySelector<HTMLDivElement>('.sort-filter')) {
          formWrapperRef.current.querySelector<HTMLDivElement>('.sort-filter').style.margin = 'auto';
        }
      }
    }
  }, [refVisible]);

  if (isLoading) {
    return (<Loader />);
  }

  if (error) {
    return (
      <Error error={error} />
    );
  }

  const onChange = (event: React.FormEvent<HTMLFormElement>) => {
    // event.preventDefault();
    setIsGlobalLoading(true);
    const delayIsSearchFilter = (event.target as HTMLFormElement).type === 'search' ? 500 : 0;

    if (timeoutSearchFilterIdRef.current) {
      clearTimeout(timeoutSearchFilterIdRef.current);
    }
    if ((event.target as HTMLFormElement).name === 'view') {
      setPageLayout((event.target as HTMLFormElement).value as LayoutFormViewType);
      setIsGlobalLoading(false);
      return;
    }

    timeoutSearchFilterIdRef.current = setTimeout(async () => {
      await queryClient.refetchQueries(['media', 'list']);
      setIsGlobalLoading(false);
    }, delayIsSearchFilter);
  };

  return (
    <div
      className="ranky-media__filters"
      ref={(el) => {
        formWrapperRef.current = el;
        setRefVisible(!!el);
      }}
    >
      <form
        role="search"
        action=""
        name="media-filter"
        id="media-form-filter"
        method="GET"
        autoComplete="off"
        onChange={(event) => onChange(event)}
      >
        <SelectFilter
          data={filters.mimeTypes}
          name="mime"
          operator={FilterOperator.STARTS}
          defaultOption={{ value: 'all', label: translator.trans('filters_mime_type') }}
        />
        <SelectFilter
          data={filters.users}
          name="createdBy"
          operator={FilterOperator.EQUALS}
          defaultOption={{ value: 'all', label: translator.trans('filters_user') }}
        />
        <SelectFilter
          data={filters.availableDates}
          name="createdAt"
          operator={FilterOperator.EQUALS}
          defaultOption={{ value: 'all', label: translator.trans('filters_date') }}
        />
        <InputSearchFilter name="name" placeholder={translator.trans('filters_search')} />
        {pageLayout !== LayoutFormView.SELECTABLE ? <LayoutFilter /> : null}
        <SortFilter />
      </form>
    </div>
  );
};

export default FormFilters;
