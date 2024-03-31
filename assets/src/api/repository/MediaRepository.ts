import BaseRepository from '@rankyMedia/api/repository/BaseRepository';

export default class MediaRepository extends BaseRepository {
  constructor(public readonly apiPrefix: null | string) {
    super();
  }

  public getRoutes() {
    return {
      config: `${this.apiPrefix}/ranky/media/config`,
      filters: `${this.apiPrefix}/ranky/media/filters`,
      all: `${this.apiPrefix}/ranky/media`,
      upload: `${this.apiPrefix}/ranky/media`,
      filter: (queryString: string) => `${this.apiPrefix}/ranky/media${queryString}`,
      get: (id: string) => `${this.apiPrefix}/ranky/media/${id}`,
      delete: (id: string) => `${this.apiPrefix}/ranky/media/${id}`,
      update: (id: string) => `${this.apiPrefix}/ranky/media/${id}`,
    };
  }

  public static create(apiPrefix = '') {
    return new MediaRepository(apiPrefix);
  }

  async get<T>(id: string) {
    return this.fetcher<T>(this.getRoutes().get(id), 'GET');
  }

  async filter<T>(queryString: string) {
    return this.fetcher<T>(this.getRoutes().filter(queryString), 'GET');
  }

  async filters<T>() {
    return this.fetcher<T>(this.getRoutes().filters, 'GET');
  }

  async all<T>() {
    return this.fetcher<T>(this.getRoutes().all, 'GET');
  }

  async delete<T>(id: string) {
    return this.fetcher<T>(this.getRoutes().delete(id), 'DELETE');
  }

  async post<T>(data: object | FormData | null) {
    return this.fetcher<T>(this.getRoutes().upload, 'POST', data);
  }

  async put<T>(id: string, data: object | FormData | null) {
    return this.fetcher<T>(this.getRoutes().update(id), 'PUT', data);
  }

  async bulkDelete(items: string[]) {
    const response = await Promise.all(items.map((id) => this.delete<{ message: string }>(id)));
    const data = response.reduce((obj, item) => Object.assign(obj, item?.data), {});
    const error = response.filter((item) => item.error).map((item) => item.error)[0] || null;
    return {
      data,
      error,
    };
  }
}
