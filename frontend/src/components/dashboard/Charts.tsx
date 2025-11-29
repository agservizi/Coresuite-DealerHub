"use client";

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
    <div className="grid gap-6 lg:grid-cols-3">
      <div className="glass-card col-span-2 p-4">
        <p className="mb-2 text-sm text-slate-400">Contratti per data</p>
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
      <div className="glass-card p-4">
        <p className="mb-2 text-sm text-slate-400">Contratti per tipo servizio</p>
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
        />
      </div>
      <div className="glass-card col-span-3 p-4">
        <p className="mb-2 text-sm text-slate-400">Contratti per gestore</p>
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
        />
      </div>
    </div>
  );
}
