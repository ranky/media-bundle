import React from 'react';
import { MediaContextType, AppType, ConfigResponseType } from '@rankyMedia/types/Config';
import Loader from '@rankyMedia/components/Common/Loader/Loader';
import Error from '@rankyMedia/components/Common/Error';
import Settings from '@rankyMedia/api/model/Settings';
import { fetcher } from '@rankyMedia/helpers/fetch';
import { useQuery } from 'react-query';
import Translator from '@rankyMedia/api/model/Translator';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';
import { LayoutFormView } from '@rankyMedia/types/PageLayout';
import { pageLayoutState } from '@rankyMedia/states/state';
import { Provider } from 'jotai';
import MediaRepository from '@rankyMedia/api/repository/MediaRepository';

const MediaContext = React.createContext<MediaContextType>({} as MediaContextType);

const MediaProvider = (
  {
    children, apiPrefix, selectionMode = false, multipleSelection = false, title = null, targetRef = null,
  }: AppType,
) => {
  let config = null;
  const mediaRepository = MediaRepository.create(apiPrefix);
  const routes = mediaRepository.getRoutes();
  const { data, error, isLoading } = useQuery<ConfigResponseType, ApiProblemError>(
    'config',
    () => fetcher(routes.config),
  );

  if (isLoading) {
    return (<Loader />);
  }

  if (error) {
    if (!(error instanceof ApiProblemError)) {
      console.error(error);
      const newError = new ApiProblemError(error, 400);
      return (<Error error={newError} />);
    }
    return (<Error error={error} />);
  }

  Settings.fromApi(data);
  Settings.apiPrefix = apiPrefix || '';
  Settings.selectionMode = selectionMode;
  Settings.multipleSelection = multipleSelection;
  Settings.title = title;
  Settings.targetRef = targetRef;

  config = {
    settings: Settings.getInstance(),
    translator: Translator.fromApi(data),
    mediaRepository,
  };

  return (
    <Provider initialValues={[[pageLayoutState, selectionMode ? LayoutFormView.SELECTABLE : LayoutFormView.GRID]]}>
      <MediaContext.Provider value={config}>
        {children}
      </MediaContext.Provider>
    </Provider>
  );
};

export { MediaProvider };

export default MediaContext;
