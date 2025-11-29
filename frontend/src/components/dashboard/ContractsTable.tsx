"use client";

import { ArrowUpRight } from "lucide-react";
import Link from "next/link";
import type { ContractSummary } from "@/types";

export function ContractsTable({ contracts }: { contracts: ContractSummary[] }) {
  return (
    <div className="glass-card overflow-hidden">
      <div className="flex items-center justify-between border-b border-white/5 px-4 py-2 text-sm text-gray-400">
        <span>Ultimi contratti</span>
        <Link href="/contracts" className="inline-flex items-center gap-1 text-emerald-300">
          Vedi tutti <ArrowUpRight size={16} />
        </Link>
      </div>
      <div className="divide-y divide-white/5">
        {contracts.map((contract) => (
          <div key={contract.id} className="flex flex-wrap items-center gap-3 px-4 py-3 text-sm">
            <div className="flex-1">
              <p className="font-semibold text-white">{contract.customerName}</p>
              <p className="text-xs uppercase text-gray-400">
                {contract.serviceType} • {contract.provider}
              </p>
            </div>
            <span className="rounded-full bg-white/10 px-3 py-1 text-xs">
              {contract.status}
            </span>
            <span className="text-xs text-gray-400">
              {new Intl.DateTimeFormat("it-IT", {
                day: "2-digit",
                month: "short",
              }).format(new Date(contract.createdAt))}
            </span>
          </div>
        ))}
        {contracts.length === 0 && (
          <p className="px-4 py-6 text-center text-sm text-gray-400">
            Nessun contratto disponibile.
          </p>
        )}
      </div>
    </div>
  );
}
