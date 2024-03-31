import payloadTransform from '@rankyMedia/helpers/payload';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';

export type FetcherProps<T> = {
  data: T,
  error: null | ApiProblemError
};

export default class BaseRepository {
  public static readonly defaultHeaders = {
    'X-Requested-With': 'XMLHttpRequest', // Required by Symfony
    'Content-Type': 'application/json',
  };

  // eslint-disable-next-line class-methods-use-this
  protected async fetcher<T>(
    url: string,
    method = 'GET',
    payload: null | FormData | URLSearchParams | object = null,
    headers = {},
  ): Promise<FetcherProps<T>> {
    let error = null;
    const options = {
      method,
      credentials: 'same-origin',
      headers: new Headers({ ...BaseRepository.defaultHeaders, ...headers }),
    } as RequestInit;
    let finalUrl = url;

    if (payload instanceof URLSearchParams) {
      finalUrl += finalUrl.indexOf('?') >= 0 ? '&' : '?';
      finalUrl += payload.toString();
    } else if (payload instanceof FormData || payload instanceof Object) {
      options.body = await payloadTransform(payload);
    }

    const response = await fetch(finalUrl, options);
    const data = await response.json();

    if (!response.ok) {
      if (!data?.title) {
        const message = `Error ${response.status}: ${response.statusText} when fetch ${url}`;
        error = new ApiProblemError(message);
        error.data = data;
        error.status = response.status;
      } else {
        error = ApiProblemError.fromApiProblem(data);
      }
    }
    return {
      data,
      error,
    };
  }
}
