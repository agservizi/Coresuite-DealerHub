"use client";

import { useSearchParams } from "next/navigation";
import { useState } from "react";
import type { FormEvent } from "react";
import { fetchCoverage } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import type { CoverageRequest, CoverageResponse } from "@/types";

export function CoverageForm() {
  const params = useSearchParams();
  const operator = params.get("operator") ?? "FASTWEB";
  const { token } = useAuth();
  const [result, setResult] = useState<CoverageResponse | null>(null);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    if (!token) return;
    setLoading(true);
    const payload: CoverageRequest = {
      operator: operator as CoverageRequest["operator"],
      address: String(formData.get("address")),
      city: String(formData.get("city")),
      zipCode: String(formData.get("zipCode")),
    };
    try {
      const response = await fetchCoverage(token, payload);
      setResult(response);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="grid gap-6 lg:grid-cols-2">
      <form className="glass-card p-5" onSubmit={handleSubmit}>
        <p className="mb-4 text-sm text-gray-400">
          Inserisci indirizzo per controllare la copertura {operator}.
        </p>
        <label className="text-sm">
          Indirizzo
          <input
            name="address"
            className="mt-1 w-full rounded-lg border border-white/10 bg-white/5 p-2 text-white"
            required
          />
        </label>
        <label className="mt-3 text-sm">
          Città
          <input
            name="city"
            className="mt-1 w-full rounded-lg border border-white/10 bg-white/5 p-2 text-white"
            required
          />
        </label>
        <label className="mt-3 text-sm">
          CAP
          <input
            name="zipCode"
            className="mt-1 w-full rounded-lg border border-white/10 bg-white/5 p-2 text-white"
            required
          />
        </label>
        <button
          type="submit"
          className="mt-4 w-full rounded-xl bg-sky-500 py-2 font-semibold text-white"
          disabled={loading}
        >
          {loading ? "Ricerca in corso..." : "Verifica"}
        </button>
      </form>
      <div className="glass-card p-5">
        {!result && <p className="text-sm text-gray-400">Nessuna verifica effettuata.</p>}
        {result && (
          <div>
            <p className="text-sm text-gray-400">Risultato</p>
            <p className="text-2xl font-semibold text-white">{result.available ? "Copertura disponibile" : "Non disponibile"}</p>
            <p className="mt-2 text-sm text-gray-300">
              Tecnologie: {result.technologies.join(", ")}
            </p>
            {result.notes && (
              <p className="mt-2 text-xs text-gray-400">{result.notes}</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
