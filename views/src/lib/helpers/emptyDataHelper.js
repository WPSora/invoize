// set to this for empty data
export const emptyData = [{ name: "none" }];

/**
 * to check for ARRAY only, not for OBJECT
 * @param {Array} arr 
 * @returns boolean; 
 */
export const isEmptyCheck = (arr) => {
  if ((arr.length === 1 && arr[0].name === 'none') || (arr.length === 0)) {
    return true;
  }
  return false;
}