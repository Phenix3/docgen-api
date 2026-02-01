import React, { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useTemplates, useTemplate } from '../hooks/useTemplates';
import { useCreateDocument } from '../hooks/useDocuments';
import { useAuth } from '../hooks/useAuth';
import { documentSchema } from '../utils/validators';
import { Button, Input, Select, Card, CardBody, Alert } from '../components/common';

export default function DocumentNew() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const preselectedTemplateId = searchParams.get('template');
  
  const { user } = useAuth();
  const { data: templates = [] } = useTemplates();
  const [selectedTemplateId, setSelectedTemplateId] = useState(preselectedTemplateId);
  
  const { data: selectedTemplate, isLoading: loadingTemplate } = useTemplate(selectedTemplateId);
  const createMutation = useCreateDocument();

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors, isSubmitting },
  } = useForm({
    resolver: zodResolver(documentSchema),
    defaultValues: {
      templateId: preselectedTemplateId ? parseInt(preselectedTemplateId) : '',
      title: '',
      inputData: {},
    },
  });

  // Watch templateId change
  const watchedTemplateId = watch('templateId');
  useEffect(() => {
    if (watchedTemplateId) {
      setSelectedTemplateId(watchedTemplateId);
    }
  }, [watchedTemplateId]);

  const onSubmit = async (data) => {
    try {
      // Ensure templateId is a number
      data.templateId = parseInt(data.templateId);
      
      const result = await createMutation.mutateAsync(data);
      navigate(`/documents/${result.id}`);
    } catch (error) {
      // Error handled by mutation hook
    }
  };

  const templateOptions = templates.map(t => ({
    value: t.id,
    label: t.name
  }));

  // Render dynamic fields based on template schema
  const renderFields = () => {
    if (!selectedTemplate || !selectedTemplate.fields) return null;

    return selectedTemplate.fields.map((field) => {
      const fieldName = `inputData.${field.name}`;
      
      switch (field.type) {
        case 'text':
        case 'email':
        case 'tel':
        case 'date':
          return (
            <Input
              key={field.name}
              label={field.label}
              type={field.type}
              placeholder={field.placeholder}
              required={field.required}
              helpText={field.description}
              error={errors.inputData?.[field.name]?.message}
              {...register(fieldName)}
            />
          );
        case 'textarea':
          // We can add a Textarea component later, using Input for now
           return (
            <div key={field.name} className="w-full">
               <label className="form-label">
                  {field.label}
                  {field.required && <span className="text-danger-500 ml-1">*</span>}
                </label>
               <textarea
                 className="form-input"
                 rows={4}
                 placeholder={field.placeholder}
                 {...register(fieldName)}
               />
               {field.description && <p className="form-help">{field.description}</p>}
            </div>
           );
        case 'select':
           return (
             <Select
               key={field.name}
               label={field.label}
               options={field.options?.map(opt => ({ value: opt, label: opt })) || []}
               required={field.required}
               helpText={field.description}
               {...register(fieldName)}
             />
           );
        default:
          return null;
      }
    });
  };

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-secondary-900">Nouveau document</h1>
        <p className="text-secondary-600">
          Remplissez le formulaire ci-dessous pour générer votre document
        </p>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        <Card>
          <CardBody className="space-y-6">
            <Select
              label="Type de document"
              options={templateOptions}
              placeholder="Sélectionnez un modèle..."
              error={errors.templateId?.message}
              {...register('templateId')}
            />

            <Input
              label="Titre du document"
              placeholder="Ex: Demande de stage - Google"
              error={errors.title?.message}
              {...register('title')}
            />
          </CardBody>
        </Card>

        {selectedTemplate && (
          <Card>
            <div className="border-b border-secondary-200 px-6 py-4 bg-secondary-50">
              <h3 className="font-medium text-secondary-900">Informations requises</h3>
              <p className="text-sm text-secondary-500 mt-1">
                Ces informations seront utilisées pour générer le document
              </p>
            </div>
            <CardBody className="space-y-6">
              {loadingTemplate ? (
                <div className="text-center py-8">Chargement du formulaire...</div>
              ) : (
                renderFields()
              )}
            </CardBody>
          </Card>
        )}

        <div className="flex justify-end gap-4">
          <Button
            type="button"
            variant="ghost"
            onClick={() => navigate('/documents')}
          >
            Annuler
          </Button>
          <Button
            type="submit"
            isLoading={isSubmitting || createMutation.isPending}
            disabled={!selectedTemplate}
          >
            Créer le document
          </Button>
        </div>
      </form>
    </div>
  );
}
