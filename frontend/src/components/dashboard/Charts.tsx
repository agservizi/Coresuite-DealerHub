"use client";

import React from "react";
import { Chart as ChartJS, ArcElement, BarElement, CategoryScale, Legend, LineElement, LinearScale, PointElement, Tooltip } from "chart.js";
import { Bar, Doughnut, Line } from "react-chartjs-2";
import type { ChartSeries } from "@/types";

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Tooltip, Legend);

interface Props {
  bars: ChartSeries;
  lines: ChartSeries;
  donut: ChartSeries;
}

export function DashboardCharts({ bars, lines, donut }: Props) {
  return (
    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div className="glass-card col-span-1 md:col-span-2 lg:col-span-2 p-4 h-48 md:h-64 lg:h-80 overflow-hidden">
        <p className="mb-2 text-sm text-gray-500">Contratti per data</p>
        <div className="h-full">
          <Line
            className="bg-transparent"
            data={{
              labels: bars.data.map((_, index) => `Settimana ${index + 1}`),
              datasets: [
                {
                  label: lines.label,
                  data: lines.data,
                  borderColor: lines.color,
                  backgroundColor: `${lines.color}55`,
                  fill: true,
                },
              ],
            }}
            options={{ responsive: true, maintainAspectRatio: false }}
          />
        </div>
      </div>
      <div className="glass-card col-span-1 p-4 h-48 md:h-64 lg:h-80 overflow-hidden">
        <p className="mb-2 text-sm text-gray-500">Contratti per tipo servizio</p>
        <div className="h-full">
          <Doughnut
            data={{
              labels: ["Mobile", "Fibra", "Luce", "Gas"],
              datasets: [
                {
                  label: donut.label,
                  data: donut.data,
                  backgroundColor: ["#22d3ee", "#6366f1", "#f97316", "#22c55e"],
                },
              ],
            }}
            options={{
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    boxWidth: 12,
                    padding: 10,
                  },
                },
              },
            }}
          />
        </div>
      </div>
      <div className="glass-card col-span-1 md:col-span-2 lg:col-span-3 p-4 h-48 md:h-64 lg:h-80 overflow-hidden">
        <p className="mb-2 text-sm text-gray-500">Contratti per gestore</p>
        <div className="h-full">
          <Bar
            data={{
              labels: ["Fastweb", "WindTre", "Iliad", "Vodafone"],
              datasets: [
                {
                  label: bars.label,
                  data: bars.data,
                  backgroundColor: bars.color,
                },
              ],
            }}
            options={{
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: {
                  ticks: {
                    maxRotation: 45,
                    minRotation: 0,
                  },
                },
                y: {
                  beginAtZero: true,
                },
              },
            }}
          />
        </div>
      </div>
    </div>
  );
}
