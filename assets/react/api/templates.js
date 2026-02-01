import apiClient, { getHydraData } from './client';

export const templatesApi = {
  /**
   * Get all templates
   */
  async getAll(params = {}) {
    const response = await apiClient.get('/templates', { params });
    return getHydraData(response);
  },
  
  /**
   * Get templates by country
   */
  async getByCountry(countryId) {
    const response = await apiClient.get('/templates', {
      params: { 'country.id': countryId },
    });
    return getHydraData(response);
  },
  
  /**
   * Get single template with fields schema
   */
  async getOne(id) {
    const response = await apiClient.get(`/templates/${id}`);
    return response.data;
  },
};

export const countriesApi = {
  /**
   * Get all active countries
   */
  async getAll() {
    const response = await apiClient.get('/countries');
    return getHydraData(response);
  },
};

export const documentTypesApi = {
  /**
   * Get all document types
   */
  async getAll() {
    const response = await apiClient.get('/document_types');
    return getHydraData(response);
  },
};
