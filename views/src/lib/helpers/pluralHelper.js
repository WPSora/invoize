/**
 * ex: 7 day -> return 7 days
 * ex: 1 day -> return 1 day
 * @param {string} str 
 * @returns  {string}
 */
export const pluralHelper = (str) => {
  const arr = str.split(" ");
  const num = parseInt(arr[0]);
  if (num > 1) {
    return `${str}s`;
  }
  return str;
};