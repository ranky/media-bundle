import React from 'react';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';
import appConfig from '@rankyMedia/config';
import '@rankyMedia/styles/error.scss';

const Error = ({ error }: { error: ApiProblemError }): React.ReactElement => {
  return (
    <div className={`${appConfig.root_class.slice(1)}__error`}>
      <h2>❌ Error {error?.status || ''}</h2>
      <p>{error.message}</p>
      {error?.hasCauses()
        && (`
          <ul>
            ${error.causes.map((cause) => `<li>${cause.name}: ${cause.reason}</li>`)}
        </ul>
      `)}
      {error?.stack
        && (<pre><code>{error.stack}</code></pre>)}
    </div>
  );
};

export default Error;
