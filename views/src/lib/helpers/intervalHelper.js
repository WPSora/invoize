import { capitalizeFirstLetter } from "./capitalHelper";

export const formatInterval = (interval, detail) => {
  switch (interval) {
    case 'daily':
      return "";
    case 'weekly':
      return `on ${capitalizeFirstLetter(detail.on)}`;
    case 'monthly':
      return `on ${nth(detail.on)}`
    case 'yearly':
      if (detail.at === 'last') return `on the last day of ${capitalizeFirstLetter(detail.on)}`;
      return `on ${capitalizeFirstLetter(detail.on)} ${nth(detail.at)}`
    default:
      return;
  }
}

const nth = (d) => {
  if (d === 'first') return '1st';
  if (d === 'last') return 'last day of the month';
  if (d > 3 && d < 21) return `${d}th`;
  switch (d % 10) {
    case 1: return `${d}st`;
    case 2: return `${d}nd`;
    case 3: return `${d}rd`;
    default: return `${d}th`;
  }
};