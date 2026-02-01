import apiClient, { getHydraData, getHydraTotalItems } from './client';
import { downloadFile } from '../utils/helpers';

export const documentsApi = {
  /**
   * Get all documents for current user
   */
  async getAll(params = {}) {
    const response = await apiClient.get('/documents', { params });
    return {
      data: getHydraData(response),
      total: getHydraTotalItems(response),
    };
  },
  
  /**
   * Get single document
   */
  async getOne(id) {
    const response = await apiClient.get(`/documents/${id}`);
    return response.data;
  },
  
  /**
   * Create new document
   */
  async create(data) {
    const response = await apiClient.post('/documents', {
      template: `/api/templates/${data.templateId}`,
      title: data.title,
      inputData: data.inputData,
    });
    return response.data;
  },
  
  /**
   * Update document
   */
  async update(id, data) {
    const response = await apiClient.put(`/documents/${id}`, {
      title: data.title,
      inputData: data.inputData,
    });
    return response.data;
  },
  
  /**
   * Delete document
   */
  async delete(id) {
    await apiClient.delete(`/documents/${id}`);
  },
  
  /**
   * Generate document content
   */
  async generate(id, regenerate = false) {
    const response = await apiClient.post(`/documents/${id}/generate`, {
      regenerate,
    });
    return response.data;
  },
  
  /**
   * Export document to PDF
   */
  async exportPdf(id, filename) {
    const response = await apiClient.get(`/documents/${id}/export/pdf`, {
      responseType: 'blob',
    });
    downloadFile(response.data, `${filename}.pdf`);
  },
  
  /**
   * Export document to DOCX
   */
  async exportDocx(id, filename) {
    const response = await apiClient.get(`/documents/${id}/export/docx`, {
      responseType: 'blob',
    });
    downloadFile(response.data, `${filename}.docx`);
  },
};
