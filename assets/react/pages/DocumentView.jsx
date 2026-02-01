import React from 'react';
import { useParams, Link } from 'react-router-dom';
import { 
  ArrowLeftIcon, 
  ArrowDownTrayIcon, 
  PrinterIcon,
  PencilSquareIcon
} from '@heroicons/react/24/outline';
import { useDocument, useGenerateDocument, useExportDocument } from '../hooks/useDocuments';
import { Button, Card, CardBody, Spinner, Alert, Badge } from '../components/common';
import { DOCUMENT_STATUS_COLORS, DOCUMENT_STATUS_LABELS } from '../utils/constants';
import { formatDate } from '../utils/helpers';

export default function DocumentView() {
  const { id } = useParams();
  const { data: document, isLoading, isError } = useDocument(id);
  const generateMutation = useGenerateDocument();
  const exportMutation = useExportDocument();

  const handleGenerate = () => {
    generateMutation.mutate({ id, regenerate: true });
  };

  const handleExport = (format) => {
    exportMutation.mutate({ id, format, filename: document.title });
  };

  if (isLoading) {
    return (
      <div className="flex justify-center py-12">
        <Spinner size="lg" />
      </div>
    );
  }

  if (isError) {
    return (
      <Alert variant="danger" title="Erreur">
        Impossible de charger le document.
      </Alert>
    );
  }

  return (
    <div className="space-y-6 h-full flex flex-col">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex items-center gap-4">
          <Link to="/documents">
            <Button variant="ghost" size="sm" className="p-2">
              <ArrowLeftIcon className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-secondary-900">{document.title}</h1>
              <Badge variant={DOCUMENT_STATUS_COLORS[document.status]}>
                {DOCUMENT_STATUS_LABELS[document.status]}
              </Badge>
            </div>
            <p className="text-secondary-600">
              {document.template?.name} • Créé le {formatDate(document.createdAt)}
            </p>
          </div>
        </div>
        
        <div className="flex items-center gap-2">
          {document.status === 'completed' && (
            <>
              <Button 
                variant="outline" 
                leftIcon={<ArrowDownTrayIcon className="h-5 w-5" />}
                onClick={() => handleExport('pdf')}
                isLoading={exportMutation.isPending && exportMutation.variables?.format === 'pdf'}
              >
                PDF
              </Button>
              <Button 
                variant="outline"
                leftIcon={<ArrowDownTrayIcon className="h-5 w-5" />}
                onClick={() => handleExport('docx')}
                isLoading={exportMutation.isPending && exportMutation.variables?.format === 'docx'}
              >
                Word
              </Button>
            </>
          )}
          <Button 
            leftIcon={<PencilSquareIcon className="h-5 w-5" />}
            onClick={handleGenerate}
            isLoading={generateMutation.isPending}
          >
            {document.status === 'draft' ? 'Générer' : 'Régénérer'}
          </Button>
        </div>
      </div>

      {/* Content Preview */}
      <Card className="flex-1 overflow-hidden flex flex-col">
        <div className="bg-secondary-50 border-b border-secondary-200 px-4 py-2 flex justify-between items-center">
          <span className="text-sm font-medium text-secondary-600">Aperçu</span>
          <Button size="sm" variant="ghost" onClick={() => window.print()}>
            <PrinterIcon className="h-4 w-4 mr-1" /> Imprimer
          </Button>
        </div>
        <CardBody className="flex-1 overflow-y-auto bg-secondary-100 p-8">
          <div className="max-w-[21cm] mx-auto bg-white shadow-lg min-h-[29.7cm] p-[2.5cm]">
             {document.content ? (
               <div 
                 className="prose max-w-none"
                 dangerouslySetInnerHTML={{ __html: document.content }} 
               />
             ) : (
               <div className="flex flex-col items-center justify-center h-full text-secondary-400 py-20">
                 <DocumentTextIcon className="h-16 w-16 mb-4 opacity-50" />
                 <p>Le contenu du document apparaîtra ici une fois généré.</p>
               </div>
             )}
          </div>
        </CardBody>
      </Card>
    </div>
  );
}
