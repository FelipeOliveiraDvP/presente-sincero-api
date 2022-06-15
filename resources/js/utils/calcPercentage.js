const calcPercentage = (partial, total) => {
  if (partial <= 0 || total <= 0) return 0;

  return (partial * 100) / total;
};

export default calcPercentage;
