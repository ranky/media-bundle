/**
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Error#differentiate_between_similar_errors
 *
 */
export default class ApiProblemError extends Error {
  public status: number;

  public stack?: string;

  public type?: string = null;

  public data?: object = null;

  public details?: Array<string> = [];

  public causes?: Array<{ name: string, reason: string }> = [];

  constructor(message: string, status?: number, options?: ErrorOptions) {
    super(message, options);
    this.status = status || 400; // bad request
    this.name = this.constructor.name;
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, ApiProblemError);
    }
  }

  public hasCauses(): boolean {
    return this.causes.length > 0;
  }

  public causesToHtml(): string {
    const list = this.causes.map((cause) => {
      return `<li>${cause.name}: ${cause.reason}</li>`;
    });

    return `<ul>${list.join('')}</ul>`;
  }

  public static fromApiProblem(apiProblem: ApiProblem): ApiProblemError {
    const self = new ApiProblemError(apiProblem.title);
    self.status = apiProblem.status;
    self.type = apiProblem?.type;
    self.details = apiProblem?.details || [];
    self.causes = apiProblem?.causes || [];

    return self;
  }
}
