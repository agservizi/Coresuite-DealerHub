"use client";

import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { createContract } from "@/lib/api";
import { CONTRACT_STATUSES, PROVIDERS, SERVICE_TYPES } from "@/lib/constants";
import { useAuth } from "@/context/AuthContext";

const schema = z.object({
  customerName: z.string().min(2),
  customerEmail: z.string().email(),
  customerPhone: z.string().min(6),
  provider: z.string(),
  serviceType: z.enum(SERVICE_TYPES),
  status: z.enum(CONTRACT_STATUSES),
  notes: z.string().optional(),
  documentFront: z.instanceof(File).optional(),
  documentBack: z.instanceof(File).optional(),
  signedForm: z.instanceof(File).optional(),
});

export type ContractFormValues = z.infer<typeof schema>;

export function ContractForm({ onSuccess }: { onSuccess?: () => void }) {
  const { token } = useAuth();
  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
    reset,
  } = useForm<ContractFormValues>({
    resolver: zodResolver(schema),
    defaultValues: {
      serviceType: "MOBILE",
      status: "NUOVO",
      provider: PROVIDERS[0],
    },
  });

  const onSubmit = async (values: ContractFormValues) => {
    if (!token) return;
    const payload = {
      ...values,
      documentFront: values.documentFront ?? null,
      documentBack: values.documentBack ?? null,
      signedForm: values.signedForm ?? null,
    };
    await createContract(token, payload);
    reset();
    onSuccess?.();
  };

  return (
    <form className="space-y-5" onSubmit={handleSubmit(onSubmit)}>
      <div className="grid gap-4 md:grid-cols-2">
        <label className="text-sm">
          Cliente
          <input
            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
            placeholder="Mario Rossi"
            {...register("customerName")}
          />
          {errors.customerName && (
            <span className="text-xs text-rose-400">Campo obbligatorio</span>
          )}
        </label>
        <label className="text-sm">
          Email
          <input
            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
            type="email"
            {...register("customerEmail")}
          />
        </label>
        <label className="text-sm">
          Telefono
          <input
            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
            {...register("customerPhone")}
          />
        </label>
        <label className="text-sm">
          Gestore
          <select
            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
            {...register("provider")}
          >
            {PROVIDERS.map((provider) => (
              <option key={provider} value={provider}>
                {provider}
              </option>
            ))}
          </select>
        </label>
        <label className="text-sm">
          Servizio
          <select
            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
            {...register("serviceType")}
          >
            {SERVICE_TYPES.map((service) => (
              <option key={service} value={service}>
                {service}
              </option>
            ))}
          </select>
        </label>
        <label className="text-sm">
          Stato
          <select
            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
            {...register("status")}
          >
            {CONTRACT_STATUSES.map((status) => (
              <option key={status} value={status}>
                {status}
              </option>
            ))}
          </select>
        </label>
      </div>
      <label className="text-sm">
        Note
        <textarea
          rows={4}
          className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-gray-900"
          {...register("notes")}
        />
      </label>
      <div className="grid gap-4 md:grid-cols-3">
        <label className="text-sm">
          Documento fronte
          <input type="file" className="mt-2" {...register("documentFront")} />
        </label>
        <label className="text-sm">
          Documento retro
          <input type="file" className="mt-2" {...register("documentBack")} />
        </label>
        <label className="text-sm">
          Modulo firmato
          <input type="file" className="mt-2" {...register("signedForm")} />
        </label>
      </div>
      <button
        type="submit"
        className="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white transition hover:bg-emerald-400 disabled:opacity-60"
        disabled={isSubmitting}
      >
        {isSubmitting ? "Salvataggio..." : "Salva contratto"}
      </button>
    </form>
  );
}
