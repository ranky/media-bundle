export type File = {
  name: string
  url: string
  basename: string
  mime: string
  mimeType: string
  mimeSubType: string
  extension: string
  size: number
  humanSize: string
};

export type Dimension = {
  width?: number
  height?: number
  label: string
};

export type Description = {
  alt: string
  title: string
};

export type Breakpoint = 'large' | 'medium' | 'small' | 'xsmall';

export type Thumbnail = {
  breakpoint: Breakpoint
  name: string
  url: string
  size: number
  dimension: Dimension
  humanSize: string
};

export type Media = {
  id: string
  createdAt: string
  updatedAt: string
  createdBy: string
  updatedBy: string
  file: File
  dimension: Dimension
  description: Description
  thumbnails: Thumbnail[]
};

type PaginationType = {
  total: number
  count: number
  page: number
  pages: number
  limit: number
};

export type MediaResultType = {
  result: Media[],
  pagination: PaginationType,
};

export type MediaQueryType = {
  pageParams: Array<number>;
  pages: Array<MediaResultType>
};

export type MediaItem = {
  media: Media;
};
