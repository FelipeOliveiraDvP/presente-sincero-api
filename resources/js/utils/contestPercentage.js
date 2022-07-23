export const contestPercentage = (contest) => {
  let total = 0;

  if (contest === null) return total.toFixed(2);

  const {
    use_custom_percentage,
    show_percentage,
    paid_percentage,
    custom_percentage,
  } = contest.value;

  if (show_percentage) total = paid_percentage * 100;

  if (use_custom_percentage) total = custom_percentage * 100;

  return total.toFixed(2);
};
