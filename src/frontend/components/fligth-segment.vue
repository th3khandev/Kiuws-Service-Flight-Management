<template>
  <div class="flight-segment">
    <div class="flight-segment-airline">
      <div class="flight-segment-airline-logo">
        <div class="flight-segment-airline-logo-image">
          <div>
            <img :src="segment.marketingAirlineLogo" />
          </div>
        </div>
      </div>
      <div class="flight-segment-airline-name">
        {{ segment.marketingAirlineName }} - Vuelo Nº {{ segment.flightNumber }}
      </div>
    </div>
    <div class="segment-detail">
      <span>
        {{ getDurationText(segment.journeyDuration) }}
      </span>
      <div class="segment-detail-info">
        <div class="segment-detail-timeline">
          <div class="line"></div>
        </div>
        <div class="segment-detail-times">
          <div>
            <span class="segment-detail-time">
              {{ getDateTimeText(segment.departureDateTime) }}
            </span>
          </div>
          <div>
            <span class="segment-detail-time">
              {{ getDateTimeText(segment.arrivalDateTime) }}
            </span>
          </div>
        </div>
        <div class="segment-detail-route">
          <div>
            <span class="segment-detail-city">
              {{ segment.departureAirport }}
            </span>
          </div>
          <div>
            <span class="segment-detail-city">
              {{ segment.arrivalAirport }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
// helpers
import { getDurationText, getDateTimeText } from "../../helpers/flight";

export default {
  name: "FlightSegment",
  props: ["segment"],
  methods: {
    getDurationText,
    getDateTimeText,
  },
};
</script>
<style scoped>
.flight-segment {
  display: flex;
  padding: 1rem 1.5rem;
  flex-direction: column;
  background-color: #e0e3e5b0;
}

.flight-segment-airline {
  display: flex;
  margin-bottom: 0.5rem;
  padding-inline-start: 4rem;
  color: #545860;
}

.flight-segment-price {
  display: flex;
  align-items: flex-end;
  margin-left: auto;
  font-size: 16px;
  line-height: 1.5rem;
  font-weight: 400;
}

.flight-segment-airline-logo {
  display: flex;
  align-items: center;
}

.flight-segment-airline-logo-image {
  width: 100px;
}

.flight-segment-airline-logo-image img {
  margin: 0px;
}

.flight-segment-airline-name {
  display: flex;
  align-items: center;
  font-size: 0.75rem;
  line-height: 1rem;
  font-weight: 400;
  margin-left: 20px;
}

.segment-detail {
  display: flex;
  align-items: center;
  padding-inline-start: 0.5rem;
}

.segment-detail span {
  display: inline-block;
  width: 65px;
  font-size: 14px;
  line-height: 1rem;
  font-weight: 400;
}

.segment-detail-info {
  display: flex;
  min-height: 4rem;
  flex-grow: 1;
}

.segment-detail-timeline {
  display: flex;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  flex-direction: column;
  justify-content: space-between;
  padding-inline-end: 1rem;
}

.segment-detail-timeline::before,
.segment-detail-timeline::after {
  content: "";
  display: block;
  width: 0.625rem;
  height: 0.625rem;
  border: 0.125rem solid #c2c9cd;
  border-radius: 100%;
  background: #fff;
}

.segment-detail-timeline .line {
  position: relative;
  display: flex;
  flex-grow: 1;
  inset-inline-start: 0.25rem;
}

.segment-detail-timeline .line::after {
  position: relative;
  content: "";
  width: 0.125rem;
  background-color: #c2c9cd;
}

.segment-detail-times,
.segment-detail-route {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
  flex-shrink: 0;
  margin-inline-end: 0.5rem;
}

.segment-detail-time,
.segment-detail-city {
  line-height: 1.5rem;
  font-weight: 400;
}
</style>
