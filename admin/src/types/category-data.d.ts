export interface CategoryData {
  id: number;
  name: string;
  slug: string;
  image: string;
}

export interface CreateCategoryData {
  name: string;
  image: file;
}
export interface UpdateCategoryData {
  name: string;
  image: file;
}
