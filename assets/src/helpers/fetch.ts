import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';

const errorResponse = (data: ApiProblem, response: Response, url): ApiProblemError => {
  if (!data?.title) {
    const message = `Error ${response.status}: ${response.statusText} when fetch ${url}`;
    const error = new ApiProblemError(message, response.status);
    error.data = data;
    return error;
  }

  return ApiProblemError.fromApiProblem(data);
};

const fetcher = async (url) => {
  const response = await fetch(url, {
    headers: new Headers({
      'X-Requested-With': 'XMLHttpRequest', // Required by Symfony
      'Content-Type': 'application/json',
    }),
  });

  if (!response.ok) {
    const data = await response.json() as ApiProblem;
    throw errorResponse(data, response, url);
  }

  return response.json();
};

const fetcherWithQueryString = async (url, paged = 1) => {
  let queryString = encodeURI(`?page[number]=${paged}`);
  const form = document.getElementById('media-form-filter') as HTMLFormElement;
  if (form) {
    queryString += `&${new URLSearchParams(new FormData(form) as never).toString()}`;
  }
  // decoding because not in the body request, reading better
  return fetcher(`${url}${decodeURI(queryString)}`);
};

export { fetcher, fetcherWithQueryString };
