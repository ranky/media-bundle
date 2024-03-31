export type Filter = {
  label: string;
  value: string;
};

export type Filters = {
  availableDates: Filter[];
  mimeTypes: Filter[];
  users: Filter[];
};
