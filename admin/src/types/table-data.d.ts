export interface Header {
  key: string;
  label: string;
}

export interface Item {
  id: number;
  [key: string]: any;
}

export interface Option {
  value: string | number;
  label: string;
}
