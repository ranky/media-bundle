import React from 'react';
import { QueryClient, QueryClientProvider } from 'react-query';
import { MediaProvider } from '@rankyMedia/context/MediaContext';
import { AppType } from '@rankyMedia/types/Config';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      staleTime: 60 * 2000,
    },
  },
});

const App: React.FC<AppType> = ({
  children, apiPrefix, selectionMode, multipleSelection, title, targetRef,
}) => {
  return (
    <QueryClientProvider client={queryClient}>
      <MediaProvider
        title={title}
        apiPrefix={apiPrefix}
        selectionMode={selectionMode}
        multipleSelection={multipleSelection}
        targetRef={targetRef}
      >
        <React.StrictMode>
          {children}
        </React.StrictMode>
      </MediaProvider>
    </QueryClientProvider>
  );
};
export default App;
