"use client";

import { useState } from "react";
import type { FormEvent } from "react";
import { API_BASE_URL } from "@/lib/constants";

export function RecoverForm() {
  const [status, setStatus] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    setLoading(true);
    setStatus(null);
    const email = String(formData.get("email"));
    try {
      const response = await fetch(`${API_BASE_URL}/auth.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "recover", email }),
      });
      const payload = await response.json();
      if (!response.ok) throw new Error(payload.message);
      setStatus("Email inviata con istruzioni");
    } catch (error) {
      setStatus((error as Error).message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form className="space-y-4" onSubmit={handleSubmit}>
      <label className="text-sm">
        Email
        <input
          name="email"
          type="email"
          required
          className="mt-1 w-full rounded-xl border border-gray-300 bg-gray-50 p-3 text-gray-900"
        />
      </label>
      <button
        type="submit"
        className="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white"
        disabled={loading}
      >
        {loading ? "Invio..." : "Recupera password"}
      </button>
      {status && <p className="text-center text-sm text-emerald-300">{status}</p>}
    </form>
  );
}
