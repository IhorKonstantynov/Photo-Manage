module.exports = (sequelize, Sequelize) => {
  const Photo = sequelize.define("photos", {
    user_id: {
      type: Sequelize.NUMBER
    },
    request_id: {
      type: Sequelize.NUMBER
    },
    status: {
      type: Sequelize.NUMBER
    },
    count: {
      type: Sequelize.NUMBER
    },
    result_urls: {
      type: Sequelize.TEXT
    },
    plan_id: {
      type: Sequelize.NUMBER
    }
  }, { timestamps: false });

  return Photo;
};