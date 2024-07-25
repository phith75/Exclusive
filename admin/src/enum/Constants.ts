export enum STATUS_USER_OPTIONS {
  "Active" = 1,
  "Inactive" = 2,
}
export enum ROLE_OPTIONS {
  "Admin" = 1,
  "User" = 2,
}

export enum GENDER_OPTIONS {
  "Male" = 1,
  "Female" = 2,
  "Other" = 3,
}
export enum STATUS_TICKET_OPTIONS {
  "Approved" = 1,
  "Returned" = 2,
}

export enum STATUS_TICKET_DETAIL_OPTIONS {
  "Borrowed" = 1,
  "Returned" = 2,
  "Overdue" = 3,
  "Lost" = 4,
}

export enum STATUS_REVIEW_OPTIONS {
  "Approved" = 1,
  "New" = 2,
}

export function convertEnumToOptions(
  enumObj: any
): Array<{ value: string; label: string }> {
  const options = [];

  for (const key in enumObj) {
    if (isNaN(Number(key))) {
      options.push({ value: String(enumObj[key]), label: key });
    }
  }

  return options;
}
