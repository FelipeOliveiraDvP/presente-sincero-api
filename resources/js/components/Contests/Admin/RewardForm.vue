<template>
  <div>
    <h3>Premiações</h3>

    <b-row v-for="(reward, index) in contest.rewards" :key="`reward-${index}`">
      <b-col md="2">
        <b-form-group
          id="reward_number"
          label="Número"
          label-for="reward_number"
        >
          <b-form-input
            id="reward_number"
            v-model="reward.number"
            type="number"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col md="8">
        <b-form-group
          id="reward_description"
          label="Descrição"
          label-for="reward_description"
        >
          <b-form-input
            id="reward_description"
            v-model="reward.description"
            type="text"
            :state="null"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col v-if="index > 0" md="2" class="d-flex align-items-end">
        <b-button variant="danger" @click="removeReward(index)">
          <font-awesome-icon icon="fa-solid fa-trash" />
        </b-button>
      </b-col>
    </b-row>

    <b-button variant="primary" class="my-4" @click="addReward"
      >Nova premiação</b-button
    >
  </div>
</template>

<script>
export default {
  props: {
    v: Object,
    contest: Object,
    validateState: Function,
    addReward: Function,
    removeReward: Function,
  },
  name: "RewardsForm",
};
</script>

<style>
</style>