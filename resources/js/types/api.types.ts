export type ApiResponse = {
  message?: string;
};

export type ErrorResponse = ApiResponse & {
  errors?: Record<string, Array<string>>;
};

export interface BaseQuery {
  page?: number;
  search?: number;
}

export interface PaginatedResponse<T> {
  current_page?: number;
  from?: number;
  to?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
  data?: Array<T>;
}
